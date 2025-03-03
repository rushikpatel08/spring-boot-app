//Hello
package com.student.student.contoller;
import org.springframework.web.bind.annotation.RequestBody;
import org.springframework.web.bind.annotation.RestController;
import org.springframework.web.bind.annotation.RequestParam;

import com.student.student.entity.Customer;
import com.student.student.service.CustomerService;

import java.util.List;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.web.bind.annotation.PostMapping;
import org.springframework.web.bind.annotation.PutMapping;
import org.springframework.web.bind.annotation.CrossOrigin;
import org.springframework.web.bind.annotation.DeleteMapping;
import org.springframework.web.bind.annotation.GetMapping;
import org.springframework.web.bind.annotation.PathVariable;



@RestController
@CrossOrigin("http://rushik-first-s3bucket.s3-website-us-east-1.amazonaws.com")

public class CustomerController {
 

    @Autowired
    private CustomerService customerService;
    
    @PostMapping("/insert")    
    public Customer InsertCustomer(@RequestBody Customer customer)
    {
       return customerService.InsertCustomer(customer);
    }

    @GetMapping("/display") 
    public List<Customer> DisplayCustomer()
    {
        return customerService.DisplayCustomer();

    }

    @GetMapping("/display/{id}")
    public Customer DisplayCustomer(@PathVariable Integer id)
    {
       return customerService.DisplayCustomer(id);
    }

    @DeleteMapping("/delete/{id}")
    public void DeleteCustomer(@PathVariable Integer id)
    {
        customerService.DeleteCustomer(id);
    }

    @PutMapping("/update")
    public Customer UpdateCustomer(@RequestBody Customer customer)
    {
        return customerService.UpdateCustomer(customer);
    }

    @GetMapping("/query")
    public List<Customer> findByName(@RequestParam String name)
    {
        return customerService.findByName(name);
    }

    @GetMapping("/department")
    public List<Customer> findByDepartment(@RequestParam String department)
    {
        return customerService.findByDepartment(department);
    }
}
